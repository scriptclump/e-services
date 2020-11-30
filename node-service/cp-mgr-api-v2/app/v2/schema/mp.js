/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mp', {
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    mp_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    mp_url: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mp_item_url: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mp_logo: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    country_code: {
      type: DataTypes.STRING(3),
      allowNull: true
    },
    price_url: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    tnc_url: {
      type: DataTypes.TEXT,
      allowNull: false
    },
    mp_disable_logo: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    mp_enable_logo: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    shipping_url: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    mp_type: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    mp_description: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    mp_class_object: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    mp_key: {
      type: DataTypes.STRING(4),
      allowNull: true
    },
    is_support: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '1'
    },
    created_by: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'mp'
  });
};
