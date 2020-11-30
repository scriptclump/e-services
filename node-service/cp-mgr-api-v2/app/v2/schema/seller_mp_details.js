/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('seller_mp_details', {
    se_detail_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_referance_name: {
      type: DataTypes.STRING(50),
      allowNull: false,
      defaultValue: '0'
    },
    description: {
      type: DataTypes.STRING(50),
      allowNull: false,
      defaultValue: '0'
    },
    market_place_user_name: {
      type: DataTypes.STRING(50),
      allowNull: false,
      defaultValue: '0'
    },
    market_place_password: {
      type: DataTypes.STRING(50),
      allowNull: false,
      defaultValue: '0'
    },
    warehouse_id: {
      type: DataTypes.INTEGER(5),
      allowNull: false,
      defaultValue: '0'
    },
    sellername: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'seller_mp_details'
  });
};
