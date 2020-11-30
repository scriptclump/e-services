/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('container_master', {
    crate_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    crate_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    s_no: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    length: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    breadth: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    height: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    weight: {
      type: DataTypes.FLOAT,
      allowNull: true,
      defaultValue: '0.00000'
    },
    weight_uom: {
      type: DataTypes.INTEGER(10),
      allowNull: true,
      defaultValue: '86002'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '136001'
    },
    transaction_status: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '137001'
    },
    container_type: {
      type: DataTypes.ENUM('crates','bags','cfc'),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'container_master'
  });
};
