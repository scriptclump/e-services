/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('promotion_template', {
    prmt_tmpl_Id: {
      type: DataTypes.INTEGER(10).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    prmt_tmpl_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    offer_type: {
      type: DataTypes.CHAR(50),
      allowNull: true
    },
    offer_on: {
      type: DataTypes.CHAR(50),
      allowNull: true
    },
    status: {
      type: DataTypes.CHAR(50),
      allowNull: true
    },
    is_slab: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    delete_status: {
      type: DataTypes.CHAR(50),
      allowNull: true
    },
    created_by: {
      type: DataTypes.DATE,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'promotion_template'
  });
};
